#!/bin/bash

kubectl exec -i -t -n sppa-fet-dev sppa-fet-adminguiv1-deployment-7c5589c8dc-qvbfg -c admingui-deployment -- sh -c "clear; (bash || ash || sh)"

