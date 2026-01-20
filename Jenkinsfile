pipeline {
    agent { label 'jenkins-agent' }

    environment {
        PROD_DIR = "/home/jenkins-agent/student-app"
        TEST_URL = "http://localhost:3000"
        API_URL  = "http://localhost:3000/api/students.php"
    }

    stages {

        stage('Cleanup Workspace') {
            steps {
                cleanWs()
            }
        }

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('SonarQube Analysis') {
            steps {
                script {
                    def scannerHome = tool 'SonarScanner'
                    withSonarQubeEnv('SonarServer') {
                        sh """
                        ${scannerHome}/bin/sonar-scanner \
                          -Dsonar.projectKey=student-management-php \
                          -Dsonar.projectName='Student Management PHP' \
                          -Dsonar.sources=frontend/src,backend/src \
                          -Dsonar.php.exclusions=**/vendor/** \
                          -Dsonar.sourceEncoding=UTF-8
                        """
                    }
                }
            }
        }

        stage('Build & Push Docker Images') {
            steps {
                script {
                    sh 'docker build -t h8815/student-app-frontend:latest ./frontend'
                    sh 'docker build -t h8815/student-app-backend:latest ./backend'

                    withCredentials([
                        usernamePassword(
                            credentialsId: 'dockerhub-credentials-h8815',
                            usernameVariable: 'DOCKER_USER',
                            passwordVariable: 'DOCKER_PASS'
                        )
                    ]) {
                        sh '''
                          echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin
                          docker push h8815/student-app-frontend:latest
                          docker push h8815/student-app-backend:latest
                        '''
                    }
                }
            }
        }

        stage('Deploy to Production') {
            steps {
                script {
                    withCredentials([
                        file(credentialsId: '3TIER-PHP', variable: 'ENVFILE')
                    ]) {

                        sh '''
                        set -e

                        # Prepare env file
                        cp "$ENVFILE" .env

                        # Stop running stack (if any)
                        if [ -d "${PROD_DIR}" ]; then
                          cd ${PROD_DIR} && docker compose down || true
                        fi

                        # 🔥 HARD RESET PROD DIRECTORY (THIS FIXES init.sql ISSUE)
                        rm -rf ${PROD_DIR}
                        mkdir -p ${PROD_DIR}/nginx

                        # Copy deployment artifacts fresh
                        cp docker-compose.yml .env ${PROD_DIR}/
                        cp -r init ${PROD_DIR}/
                        cp nginx/default.conf ${PROD_DIR}/nginx/

                        # Ownership sanity (important)
                        chown -R jenkins-agent:jenkins-agent ${PROD_DIR}

                        # Start stack
                        cd ${PROD_DIR}
                        docker compose pull
                        docker compose up -d --force-recreate
                        '''
                    }
                }
            }
        }

        stage('Validation') {
            steps {
                echo 'Validating Deployment...'
                sh '''
                  sleep 5
                  curl -s ${API_URL} | head -c 200
                '''
            }
        }
    }

    post {
        always {
            echo 'Cleaning up Docker artifacts on Azure VM...'
            sh 'docker image prune -f || true'
        }
        success {
            echo '✅ Pipeline and Deployment Succeeded!'
        }
        failure {
            echo '❌ Pipeline Failed.'
        }
    }
}
