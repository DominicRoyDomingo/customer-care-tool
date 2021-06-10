import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let language = i18n.locale;
let params = '?lang=' + language;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF TAGS
export function fetchList() {
  let url = '/admin/publishing-tools/categories/getList' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// POST NEW CATEGORY
export function createCategory( data ) {
  // console.log(data);
    let url = '/admin/publishing-tools/categories' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE CATEGORY
export function updateCategory( data, id ) {
  // console.log(language)
  let url = '/admin/publishing-tools/categories/'+ id;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE CATEGORY
export function softDelete( id ) {
  console.log(id)
  let url = '/admin/publishing-tools/categories/' + id + params;
  return axios.delete(url).then(response => {
    return response.data
  })
}


// GET CATEGORY NAME WHEN EDITING
export function getCategoryName( id, lang ) {
  let url = '/admin/publishing-tools/categories/getCategoryName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}