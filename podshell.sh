#!/bin/bash

kubectl exec -i -t -n sppa-fet-dev sppa-fet-adminguiv1-deployment-7f9479f476-x7jl9 -c admingui-deployment -- sh -c "clear; (bash || ash || sh)"

