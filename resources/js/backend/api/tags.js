import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let language = i18n.locale;
let params = '?lang=' + language;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF TAGS
export function fetchList() {
  let url = '/admin/publishing-tools/tags/getList' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// POST NEW TAGS
export function createTags( data ) {
    let url = '/admin/publishing-tools/tags/postTags' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE TAGS
export function updateTags( data ) {
  let url = '/admin/publishing-tools/tags/updateTags' + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE TAGS
export function softDelete( id ) {
  let url = '/admin/publishing-tools/tags/deleteTags/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET TAGS NAME WHEN EDITING
export function getTagsName( id, lang ) {
  let url = '/admin/publishing-tools/tags/getTagsName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}