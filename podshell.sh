#!/bin/bash

kubectl exec -i -t -n sppa-fet-dev sppa-fet-adminguiv1-deployment-797968cc79-zlk9s -c admingui-deployment -- sh -c "clear; (bash || ash || sh)"

