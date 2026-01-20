pipeline {
    agent { label 'jenkins-agent' }

    environment {
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

                        cd "$WORKSPACE"

                        # Prepare env
                        cp "$ENVFILE" .env

                        echo "Deploying from:"
                        pwd
                        ls -l init/init.sql

                        # Stop old stack
                        docker compose down || true

                        # Start fresh
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
