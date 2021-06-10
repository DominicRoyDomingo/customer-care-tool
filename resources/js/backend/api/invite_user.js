import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let language = i18n.locale;
let params = '?lang=' + language;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

export function inviteUser( data ) {
    let url = '/admin/auth/user/invite' + params;
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}


