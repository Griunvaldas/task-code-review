#!/bin/bash

USER_NAME=${SUDO_USER:-$USER}
echo HOST_USER_ID=$(id -u $USER_NAME) > .docker-user.env
echo HOST_GROUP_ID=$(id -g $USER_NAME) >> .docker-user.env