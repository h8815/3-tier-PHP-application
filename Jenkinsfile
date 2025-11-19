pipeline{
    agent any{
        environment{
            EC2_IP = "13.53.75.60"
            // DOCKER_USERNAME = "h8815"
            APP_PATH = "/home/ubuntu/3-tier-PHP-application"
        }

        stages{
            stage('Checkout'){
                steps{
                    echo 'Checking out source code...'
                    checkout scm
                }
            }
            stage('Build'){
                steps{
                    echo 'Building...'
                    sh '''
                    cd ${APP_PATH}
                    docker-compose up -d --build
                    '''
                }
            }
            stage('Validation'){
                steps{
                    echo 'Validate application and api using curl...'
                    script{
                        if ( curl -I "http://${EC2_IP}/" | grep "200 OK" ) && ( curl -I "http://${EC2_IP}/api/students.php" | grep "success") {
                            echo "application is ok and running"
                        }
                    }
                }
            }
            stage('Push image to DockerHub'){
                steps{
                    withCredentials([usernamePassword(credentialsId: 'dockerhub-credentials', usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]){
                        sh ''' 
                        docker login -u ${DOCKER_USER} -p ${DOCKER_PASS} 
                        docker push ${DOCKER_USER}/student-app-frontend:latest
                        docker push ${DOCKER_USER/student-app-backend:latest
                        '''
                    }
                }
            }
        }
    }
}
    
