#!/bin/bash
date +"%T" > foo && git add foo && git commit -m"bar" && git push