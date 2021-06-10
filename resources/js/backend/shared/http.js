import axios from 'axios';

import api from './api.js';


const http = axios.create({
  baseURL: api.services
});

http.CancelToken = axios.CancelToken;

http.isCancel = axios.isCancel;

http.interceptors.request.use(
  (config) => {

    let token = localStorage.getItem('token');

    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }

    if (config.method === 'get') {
      config.headers.get['Content-Type'] = 'application/json';
    }

    if (config.method === 'post') {
      config.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';

    }

    config.headers.post['Content-Type'] = 'multipart/form-data';

    return config;
  },

  (error) => {
    return Promise.reject(error);
  }
);

http.interceptors.response.use(function (config) {
  return config
}, function (error) {

  const status = error.response ? error.response.status : null;

  if(status === 401){
    auth.refreshToken().then(
      resp => {
      }
    ).catch(
      error =>{
        localStorage.clear();
        // window.location.reload();
        this.$router.push({
          name : 'Login'
        })
      }
    )

  }
});

export default http