import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let languange = i18n.locale;
let params = '?lang=' + languange;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF TAGS
export function fetchList(page = 1, search = '', filter) {
  let contentFilter = filter != undefined ? `&filter=${filter.filter}&filter_value=${filter.filter_value}` : ''
  let url = (search == '' || search == null) ?  '/admin/publishing-tools/content/getList' + params + `&page=${page}${contentFilter}` : '/admin/publishing-tools/content/getList' + params + `&page=${page}` + `&search=${search}`;
  return axios.get(url).then(response => {
    return response.data
  })
}

export function fetchUsers() {
  let url = '/admin/publishing-tools/content/users' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

// POST NEW CONTENT
export function createContent( data ) {
    let url = '/admin/publishing-tools/content/postContent' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE CONTENT
export function updateContent( data ) {
  let url = `/admin/publishing-tools/content/updateContent` + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE CONTENT
export function softDelete( id ) {
  let url = '/admin/publishing-tools/content/deleteContent/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET CONTENT NAME WHEN EDITING
export function getContentName( id, lang ) {
  let url = '/admin/publishing-tools/content/getContentName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}

// GET NEXT STATUS
export function fetchNextStatus(id) {
  let url = `/admin/publishing-tools/content/getNextStatus/${id}` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

// UPDATE STATUS
export function updateStatus( data ) {
  let url = `/admin/publishing-tools/content/updateStatus` + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}

// GET HISTORY
export function fetchHistory(id,content) {
  let url = `/admin/publishing-tools/content/getHistory?id=${id}`;
  return axios.get(url).then(response => {
    return response.data
  })
}

// GET ITEM STATUS
export function getItemStatus() {
  let url = `/admin/publishing-tools/content/getItemStatus` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

// GET LAST STATUS
export function getLastStatus() {
  let url = `/admin/publishing-tools/content/getLastStatus` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}






