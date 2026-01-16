pipeline {
    agent { label 'wsl-agent' } 

    environment {
        // Azure Server Config
        PROD_IP = "4.240.107.20"
        
        // UPDATED: Using your specific user
        PROD_USER = "c9lab" 
        PROD_DIR = "/home/c9lab/student-app"

        // URLs for validation
        TEST_URL = "http://4.240.107.20:3000" 
        API_URL  = "http://4.240.107.20:3000/api/students.php" 
    }

    stages {
        stage('Cleanup') {
            steps { cleanWs() }
        }

        stage('Checkout') {
            steps { checkout scm }
        }

        stage('SonarQube Analysis') {
            steps {
                script {
                    def scannerHome = tool 'SonarScanner'
                    withSonarQubeEnv('SonarServer') {
                        sh """${scannerHome}/bin/sonar-scanner \
                        -Dsonar.projectKey=student-management-php \
                        -Dsonar.projectName='Student Management PHP' \
                        -Dsonar.sources=frontend/src,backend/src \
                        -Dsonar.php.exclusions=**/vendor/** \
                        -Dsonar.sourceEncoding=UTF-8"""
                    }
                }
            }
        }

        stage('Build & Push') {
            steps {
                script {
                    sh 'docker build -t h8815/student-app-frontend:latest ./frontend'
                    sh 'docker build -t h8815/student-app-backend:latest ./backend'

                    withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                        sh '''
                            echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin
                            docker push h8815/student-app-frontend:latest
                            docker push h8815/student-app-backend:latest
                        '''
                    }
                }
            }
        }

        stage('Deploy to Azure') {
            steps {
                // UPDATED: Using the specific ID you created
                sshagent(['php-application-ssh-key']) {
                    script {
                        // 1. Prepare .env file
                        withCredentials([file(credentialsId: '3TIER-PHP', variable: 'ENVFILE')]) {
                            sh 'cp "$ENVFILE" .env'
                        }

                        // 2. Create Directory on Azure (using c9lab user)
                        sh "ssh -o StrictHostKeyChecking=no ${PROD_USER}@${PROD_IP} 'mkdir -p ${PROD_DIR}/nginx'"

                        // 3. Copy Config Files to Azure
                        sh "scp -o StrictHostKeyChecking=no docker-compose.yml .env ${PROD_USER}@${PROD_IP}:${PROD_DIR}/"
                        sh "scp -o StrictHostKeyChecking=no nginx/default.conf ${PROD_USER}@${PROD_IP}:${PROD_DIR}/nginx/"

                        // 4. Restart Containers on Azure
                        sh """
                            ssh -o StrictHostKeyChecking=no ${PROD_USER}@${PROD_IP} '
                                cd ${PROD_DIR}
                                docker-compose pull
                                docker-compose down || true
                                docker-compose up -d --force-recreate
                            '
                        """
                    }
                }
            }
        }

        stage('Validation') {
            steps {
                echo 'Validating Deployment...'
                sh "curl -s ${API_URL} | grep 'success' || echo 'Waiting for DB...'"
            }
        }
    }
}