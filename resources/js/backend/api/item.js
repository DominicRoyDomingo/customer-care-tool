import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let language = i18n.locale;
let params = '?lang=' + language;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF ITEM
export function fetchList( search = '', filter , content_id = '') {
  let itemFilter = filter != undefined ? `&filter=${filter.filter}&filter_value=${filter.filter_value}` : ''
  let searchRes = search != '' ? `&search=${search}` : ``
  let id = content_id != '' ? `&id=${content_id}` : ``
  console.log(content_id)
  let url = `/admin/publishing-tools/items/getList?lang=${language}${itemFilter}${searchRes}${id}`;
  return axios.get(url).then(response => {
    return response.data
  })
}

export function fetchUsersAndItemTypes() {
    let url = `/admin/publishing-tools/items/getUsersAndItemTypes?lang=${language}`;
    return axios.get(url).then(response => {
        return response.data
    })
}

export function getContentUsersAndItemType() {
    let url = `/admin/publishing-tools/items/getContentUsersAndItemType?lang=${language}`;
    return axios.get(url).then(response => {
        return response.data
    })
}


// POST NEW ITEM
export function createItem( data ) {
    let url = '/admin/publishing-tools/items' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}

// CREATE NEW PUBLISHING
export function createPublishing( data ) {
  let url = '/admin/publishing-tools/content/items/createPublishing' + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// UPDATE PROBLEM
export function updateItem( data ) {
  let url = '/admin/publishing-tools/content/items/update';
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE ITEM
export function softDelete( id ) {
  let url = '/admin/publishing-tools/content/items/deleteItem/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET ITEM NAME WHEN EDITING
export function getTranslatedItemName( id, lang ) {
  let url = '/admin/publishing-tools/content/items/getTranslatedItemName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}

// GET NEXT STATUS
export function fetchNextStatus(id) {
  let url = `/admin/publishing-tools/content/items/getNextStatus/${id}` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

// UPDATE STATUS
export function updateStatus( data ) {
  let url = `/admin/publishing-tools/content/items/updateStatus` + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}

// GET HISTORY
export function fetchHistory(id,content) {
  let url = `/admin/publishing-tools/content/items/getHistory?id=${id}&content=${content}`;
  return axios.get(url).then(response => {
    return response.data
  })
}

// GET LAST STATUS
export function getLastStatus() {
  let url = `/admin/publishing-tools/content/items/getLastStatus` + params;
  return axios.get(url).then(response => {
    return response.data
  })
}
