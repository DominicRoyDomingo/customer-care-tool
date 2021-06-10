import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let language = i18n.locale;
let params = '?lang=' + language;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF PUBLISHING
export function fetchList(page = 1, search = '', filter) {
  let publishingFilter = filter != undefined ? `&filter=${filter.filter}&filter_value=${filter.filter_value}` : ''
  let url = (search == '' || search == null) ?  '/admin/publishing-tools/publishing/getList' + params + `&page=${page}${publishingFilter}` : '/admin/publishing-tools/publishing/getList' + params + `&page=${page}` + `&search=${search}` ;
  return axios.get(url).then(response => {
    return response.data
  })
}


export function createPublishing( data ) {
    let url = '/admin/publishing-tools/publishing/postPublishing' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}

export function getUsersAndItems() {
  let url = `/admin/publishing-tools/publishing/getUsersAndItems?lang=${language}`;
  return axios.get(url).then(response => {
      return response.data
  })
}

export function getPublishingRelation(id) {
  let url = `/admin/publishing-tools/publishing/getPublishingRelation/${id}?lang=${language}`;
  return axios.get(url).then(response => {
      return response.data
  })
}

export function attachTags( data ) {
    let url = '/admin/publishing-tools/publishing/attachTags' + params;
    return axios.post( url, data );
}

export function removeTags( data ) {
  let url = '/admin/publishing-tools/publishing/removeTags' + params;
  return axios.post( url, data );
}


export function createLink( data ) {
  let url = '/admin/publishing-tools/publishing/createLink' + params;
  return axios.post( url, data );
}

export function updatePublishing( data ) {
  let url = `/admin/publishing-tools/publishing/updatePublishing` + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}

export function softDelete( id ) {
  let url = '/admin/publishing-tools/publishing/deletePublishing/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

export function getPublishingName( id, lang ) {
  let url = '/admin/publishing-tools/publishing/getPublishingName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}

export function fetchNextStatus(id) {
  let url = `/admin/publishing-tools/publishing/getNextStatus/${id}` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

export function getTags() {
  let url = `/admin/publishing-tools/publishing/getTags` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

export function getBrandsAndPlatformTypes() {
  let url = `/admin/publishing-tools/publishing/getBrandsAndPlatformTypes` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


export function fetchTags() {
  let url = '/admin/publishing-tools/publishing/getTags' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


export function updateStatus( data ) {
  let url = `/admin/publishing-tools/publishing/updateStatus` + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


export function fetchHistory(id,content) {
  let url = `/admin/publishing-tools/publishing/getHistory?id=${id}`;
  return axios.get(url).then(response => {
    return response.data
  })
}

// GET CONTENT NAME WHEN EDITING
export function getOtherTags() {
  let url = '/admin/publishing-tools/publishing/getOtherTags' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}




