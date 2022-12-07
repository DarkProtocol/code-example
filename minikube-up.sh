#!/bin/sh
set -e

CONTEXT=$(kubectl config current-context)

if [ $CONTEXT != 'docker-desktop' ] && [ $CONTEXT != 'minikube' ]
then
  echo "Current kubernetes context is not \"docker-desktop\" or \"minikube\""
  exit 1
fi

if ! command -v envsubst &> /dev/null
then
  echo "envsubst command does not exists. If you are on macOS, install gettext with \"brew install gettext\""
  exit 1
fi

export KUBE_NAMESPACE=example
export IMAGE_BACKEND=backend:latest
export IMAGE_PULL_POLICY_BACKEND=Never
export SERVER_NAME=api.examle.minikube.test

kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v0.43.0/deploy/static/provider/cloud/deploy.yaml

cat kubernetes/namespace.yaml | envsubst | kubectl apply -f -
kubectl create configmap config-backend --from-env-file=backend/.env.minikube --namespace=$KUBE_NAMESPACE
cat kubernetes/redis.yaml | envsubst | kubectl apply -f -
cat kubernetes/minikube/volume-postgres.yaml | envsubst | kubectl apply -f -
cat kubernetes/postgres.yaml | envsubst | kubectl apply -f -
cat kubernetes/backend.yaml | envsubst | kubectl apply -f -
cat kubernetes/minikube/ingress.yaml | envsubst | kubectl apply -f -
