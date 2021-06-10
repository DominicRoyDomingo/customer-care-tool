import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let language = i18n.locale;
let params = '?lang=' + language;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF PLATFORM
export function fetchList() {
  let url = '/admin/publishing-tools/date-status/getList' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

// GET STATUS BY CLASS
export function fetchDateStatus(name) {
  let url = `/admin/publishing-tools/date-status/getDateStatus` + params + `&class=${name}`;
  return axios.get(url).then(response => {
    return response.data
  })
}


// POST NEW Date Status
export function createDateStatus( data ) {
    let url = '/admin/publishing-tools/date-status' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE PROBLEM
export function updateDateStatus( data ) {
  let url = '/admin/publishing-tools/date-status/update';
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}

// CHANGE ORDER
export function changeOrder( data ) {
  let url = '/admin/publishing-tools/date-status/changeOrder';
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE Date Status
export function softDelete( id ) {
  let url = '/admin/publishing-tools/date-status/deleteDate/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET DATE STATUS NAME WHEN EDITING
export function getDateStatusName( id, lang ) {
  let url = '/admin/publishing-tools/date-status/getDateStatusName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}