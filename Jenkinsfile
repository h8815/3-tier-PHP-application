pipeline {
    agent any
    
    environment {
        TEST_URL = "http://localhost" 
        API_URL = "http://localhost:8081/api/students.php" 
    }

    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('Build'){
            steps{
                echo 'Building the application'
                script{
                    sh 'docker build -t h8815/student-app-frontend:latest ./frontend'
                    sh 'docker build -t h8815/student-app-backend:latest ./backend'
                }
            }
        }

        stage('Load .env') {
            steps {
                script {
                    withCredentials([file(credentialsId: '3TIER-PHP', variable: 'ENVFILE')]) {
                        sh "cp ${ENVFILE} .env"
                    }
                    echo ".env loaded"
                }
            }
        }


        stage('Deploy Test') {
            steps {
                echo 'Restarting containers...'
                sh '''
                    docker-compose down
                    docker-compose up -d 
                '''
            }
        }

        stage('Wait for Database') {
            steps {
                echo 'Waiting 30 seconds for MySQL to initialize...'
                // Essential: Give the DB time to start before testing
                sh 'sleep 30'
            }
        }

        stage('Validation') {
            steps {
                echo 'Validating application...'
                script {
                    // 1. Check Frontend (HTTP 200)
                    // we use sh(script: ..., returnStatus: true) to prevent pipeline failure if check fails immediately 
                    def frontendStatus = sh(script: "curl -s -o /dev/null -w '%{http_code}' ${TEST_URL}", returnStdout: true).trim()
                    
                    if (frontendStatus == '200') {
                        echo "✅ Frontend is reachable (HTTP 200)"
                    } else {
                        error "❌ Frontend failed with HTTP ${frontendStatus}"
                    }

                    // 2. Check API (Look for "success" in body)
                    try {
                        sh "curl -s ${API_URL} | grep 'success'"
                        echo "✅ API is returning success data"
                    } catch (Exception e) {
                        error "❌ API validation failed. 'success' not found in response."
                    }
                }
            }
        }

        stage('Push image to DockerHub') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    sh '''
                        # Log in
                        echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin
                        
                        # Push images (Make sure your docker-compose.yml actually tags them like this!)
                        docker push ${DOCKER_USER}/student-app-frontend:latest
                        docker push ${DOCKER_USER}/student-app-backend:latest
                    '''
                }
            }
        }
    }
    
    post {
        always {
            echo 'Pipeline finished.'
        }
        failure {
            echo '⚠️ Pipeline failed!'
        }
    }
}