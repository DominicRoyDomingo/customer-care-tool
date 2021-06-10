import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let languange = i18n.locale;
let params = '?lang=' + languange;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF PLATFORM
export function fetchList() {
  let url = '/admin/platform-type/getList' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// POST NEW PLATFORM
export function createPlatform( data ) {
    let url = '/admin/platform-type/postPlatform' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE PROBLEM
export function updatePlatform( data ) {
  let url = '/admin/platform-type/updatePlatform' + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE PLATFORM
export function softDelete( id ) {
  let url = '/admin/platform-type/deletePlatform/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET PLATFORM NAME WHEN EDITING
export function getPlatformName( id, lang ) {
  let url = '/admin/platform-type/getPlatformName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}