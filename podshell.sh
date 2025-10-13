#!/bin/bash

kubectl exec -i -t -n sppa-fet-dev sppa-fet-adminguiv1-deployment-c95c95b8-7xj88 -c admingui-deployment -- sh -c "(bash || ash || sh)"
