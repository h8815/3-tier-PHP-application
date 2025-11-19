pipeline {
    agent any
    
    // Environment should be at the top level, not inside agent
    environment {
        // Use 'localhost' for validation since Jenkins is running on the same server
        TEST_URL = "http://13.53.75.60/" 
        API_URL = "http://13.53.75.60:8081/api/students.php"
    }

    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('Build & Deploy Test') {
            steps {
                echo 'Building and starting containers...'
                // We do NOT cd to a custom path. We use the Jenkins Workspace.
                sh '''
                    docker-compose down
                    docker-compose up -d --build
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
                    // We do NOT use -I here, because we need to read the body text
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