import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let languange = i18n.locale;
let params = '?lang=' + languange;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF BRANDS
export function fetchList() {
  let url = '/admin/brands/getList' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// POST NEW BRANDS
export function createBrands( data ) {
    let url = '/admin/brands/postBrands' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE BRANDS
export function updateBrands( data ) {
  let url = '/admin/brands/updateBrands' + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE BRANDS
export function softDelete( id ) {
  let url = '/admin/brands/deleteBrands/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET BRANDS NAME WHEN EDITING
export function getBrandsName( id, lang ) {
  let url = '/admin/brands/getBrandsName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}


// UPLOAD BRANDS LOGO
export function uploadLogo( id, lang ) {
  let url = '/admin/brands/uploadImage' + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}