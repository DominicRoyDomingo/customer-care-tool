import Vue from 'vue';
import api from '../shared/api.js';

class Auth {

  constructor(item) {
    this.url = item.url;
  }

  check() {
    return !!localStorage.getItem("user");
  }


  isTokenActive(){
    return new Promise((resolve, reject) => {
      axios.post(this.url + 'refresh', {token: this.getToken()}).then(
        _ => {
          resolve(true)
        }
      ).catch(_ => reject(false))
    })
  }

  login(data) {
    return new Promise((resolve, reject) => {
      axios.post(this.url + 'login', data).then(
        resp => {

          localStorage.setItem("token", resp.data.token);
          // localStorage.setItem("user", btoa(JSON.stringify(resp.data.user)));

          resolve(resp)
        }
      ).catch(
        error => reject(error)
      )
    });
  }

  logout() {
    return new Promise((resolve, reject) => {
      axios.post(this.url + 'logout?path=' + api.logout_path, {token: this.getToken()}).then(
        resp => {
          localStorage.clear();
          resolve(resp);
        }
      ).catch(error => reject(error));
    });
  }

  refreshToken() {
    return new Promise((resolve, reject) => {
      axios.post(this.url + 'refresh', {token: this.getToken()}).then(
        resp => {
          resolve(resp)
        }
      ).catch(error => reject(error))
    })
  }

  getToken() {
    return localStorage.getItem("token") ? localStorage.getItem("token") : false;

  }

  getUser() {
    return localStorage.getItem("user") ? JSON.parse(atob(localStorage.getItem("user"))) : null;
  }

  /* Set User Information */
  setUser() {
    return new Promise((resolve, reject) => {
      axios.get(this.url + 'me').then(
        resp => {
          if (resp.data.user) {
            localStorage.setItem("user", btoa(JSON.stringify(data.user)));
            resolve(true);
            return true;
          }
          reject(false);
          return false;
        }
      )
    });
  }

  getRequest() {
    return new Promise(( resolve, reject ) => {
      axios.get( api.services + 'admin/test', { params: {
        path: api.host
    } } ).then(
        resp => {
          resolve( resp );
        }
      ).catch(
        error => reject( error )
      );
    })
  }

  redirect() {

    window.location.href=api.redirect_url;
    
  }

}

export default Auth;