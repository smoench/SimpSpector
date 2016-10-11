import axios from 'axios';
import {base} from "./parameters";

var instance = axios.create({
  baseURL: base,
  timeout: 5000
});

instance.interceptors.response.use(function (response) {
  if (response.headers["content-type"].indexOf('json') === -1) {
    throw "wrong content, json expected!";
  }

  return response;
}, function (error) {
  return Promise.reject(error);
});

export default instance;