import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let languange = i18n.locale;
let params = '?lang=' + languange;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

// GET ALL THE LIST OF ITEM TYPE
export function fetchList() {
  let url = '/admin/item-type/getList' + params;
  return axios.get(url).then(response => {
    return response.data
  })
}

export function sortList(data) {
  let url = '/admin/item-type/sortItemType' + params;
  return axios.post(url, data).then(response => {
    return response.data
  })
}

// POST NEW ITEM TYPE
export function createItemType( data ) {
    let url = '/admin/item-type/postItemType' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


export function searchItemType( text ) {
    let url = '/admin/item-type/searchItemType' + params;
    return axios.post( url, { search : text});
}


// UPDATE ITEM TYPE
export function updateItemType( data ) {
  let url = '/admin/item-type/updateItemType' + params;
  return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


// DELETE ITEM TYPE
export function softDelete( id ) {
  let url = '/admin/item-type/deleteItemType/' + id + params;
  return axios.get(url).then(response => {
    return response.data
  })
}


// GET ITEM TYPE NAME WHEN EDITING
export function getItemTypeName( id, lang ) {
  let url = '/admin/item-type/getItemTypeName/' + id + '/' + lang;
  return axios.get(url).then(response => {
    return response.data
  })
}