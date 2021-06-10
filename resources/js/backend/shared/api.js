const Api = {

  /*For Authentications API*/
  auth: process.env.VUE_APP_SERVICE_AUTH_URL,

  redirect_url: process.env.VUE_APP_REDIRECT_URL,

  logout_path: process.env.VUE_APP_LOGOUT_PATH,
  /*For Services API*/
  services: window.location.host,

  patient_services: process.env.VUE_APP_SERVICE_PATIENT_URL,
  /*For API Host*/
  host: process.env.VUE_APP_HOST_URL,
  
  // this object requirements for api
  data: {
    host: process.env.VUE_APP_HOST_URL,
    path: process.env.VUE_APP_HOST_URL,
    data: {}
  }
};

export default Api;