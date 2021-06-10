import request from '../shared/http.js';
import api from '../shared/api.js';
import i18n from '../i18n.js';
import axios from 'axios';

let language = i18n.locale;
let params = '?lang=' + language;
// let getName = '?path=' + api.host + '&api_token=' + auth.getToken();

export function readNotification( data ) {
    let url = '/admin/auth/user/notif/read';
    return axios.post( url, data, { "content-type": "multipart/form-data" } );
}

export function userNotifs() {
    let url = `/admin/auth/user/notif`;
    return axios.get(url).then(response => {
        return response.data
    })
}

export function declineNotif(notif_id) {
    let url = `/admin/auth/user/notif/${notif_id}`;
    return axios.delete(url).then(response => {
        return response.data
    })
}


