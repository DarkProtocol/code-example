#!/bin/sh
set -e

CONTEXT=$(kubectl config current-context)

if [ $CONTEXT != 'docker-desktop' ] && [ $CONTEXT != 'minikube' ]
then
  echo "Current kubernetes context is not \"docker-desktop\" or \"minikube\""
  exit 1
fi

export KUBE_NAMESPACE=example

kubectl delete namespace $KUBE_NAMESPACE
kubectl -n $KUBE_NAMESPACE delete pv postgres