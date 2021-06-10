import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let languange = i18n.locale;
let params = '?lang=' + languange;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF PUBLISHING ITEM
export function fetchList() {
  let url = '/admin/publishing-item/getList' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// POST NEW PUBLISHING ITEM
export function createPublishingItem( data ) {
    let url = '/admin/publishing-item/postPublishingItem' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE PUBLISHING ITEM
export function updatePublishingItem( data ) {
  let url = '/admin/publishing-item/updatePublishingItem' + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE PUBLISHING ITEM
export function softDelete( id ) {
  let url = '/admin/publishing-item/deletePublishingItem/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET PUBLISHING ITEM NAME WHEN EDITING
export function getPublishingItemName( id, lang ) {
  let url = '/admin/publishing/type/getPublishingItemName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}