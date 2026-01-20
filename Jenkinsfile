pipeline {
    agent { label 'wsl-agent' }

    environment {
        // ===== Server Config (FIXED: no numeric var names) =====
        PROD_IP   = "${env.TIER3_PROD_IP}"
        PROD_USER = "${env.TIER3_PROD_USER}"
        PROD_DIR  = "/home/${env.TIER3_PROD_USER}/student-app"

        // ===== URLs =====
        TEST_URL = "http://${env.TIER3_PROD_IP}:3000"
        API_URL  = "http://${env.TIER3_PROD_IP}:3000/api/students.php"
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

        stage('Deploy to Azure') {
            steps {
                sshagent(['php-application-ssh-key']) {
                    script {

                        // Copy .env securely from Jenkins
                        withCredentials([
                            file(credentialsId: '3TIER-PHP', variable: 'ENVFILE')
                        ]) {
                            sh 'cp "$ENVFILE" .env'
                        }

                        // Create target directory
                        sh """
                        ssh -o StrictHostKeyChecking=no ${PROD_USER}@${PROD_IP} \
                        'mkdir -p ${PROD_DIR}/nginx'
                        """

                        // Copy deployment files
                        sh """
                        scp -o StrictHostKeyChecking=no docker-compose.yml .env \
                        ${PROD_USER}@${PROD_IP}:${PROD_DIR}/
                        """

                        sh """
                        scp -o StrictHostKeyChecking=no nginx/default.conf \
                        ${PROD_USER}@${PROD_IP}:${PROD_DIR}/nginx/
                        """

                        // Restart containers
                        sh """
                        ssh -o StrictHostKeyChecking=no ${PROD_USER}@${PROD_IP} '
                            cd ${PROD_DIR}
                            docker compose pull || true
                            docker compose down || true
                            docker compose up -d --force-recreate
                        '
                        """
                    }
                }
            }
        }

        stage('Validation') {
            steps {
                echo 'Validating Deployment...'
                sh """
                curl -s ${API_URL} | grep -i success \
                || echo '⚠️ API not ready yet'
                """
            }
        }
    }

    post {
        always {
            node('wsl-agent') {
                echo 'Cleaning up Docker artifacts on WSL Agent...'
                sh 'docker image prune -f || true'
            }
        }

        success {
            echo '✅ Pipeline and Deployment Succeeded!'
        }

        failure {
            echo '❌ Pipeline Failed. Check logs for details.'
        }
    }
}
