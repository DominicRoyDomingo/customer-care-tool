const api = '/api/questionnaires'
const api_questions = '/api/questions'

export function fetch_list(params) {
    return axios.get(`${api}`, { params }).then(resp => resp.data)
}

export function delete_item(params) {
    return axios.delete(`${api}`, { params }).then(resp => resp.data)
}

export function post_question(params) {
    return axios.post(`${api}`, params).then(resp => resp.data)
}

export function get_question_sequence(params) {
    return axios.get(`${api}/organizes/sequences`, { params }).then(resp => resp.data)
}

export function post_question_sequence(params) {
    return axios.post(`${api}/organizes`, params).then(resp => resp.data)
}

export function sort_question_sequence(params) {
    return axios.post(`${api}/organizes/sort`, params).then(resp => resp.data)
}

export function delete_question_sequence(params) {
    return axios.delete(`${api}/organizes`, { params }).then(resp => resp.data)
}

export function get_questions(params) {
    return axios.get(`${api_questions}`, { params }).then(resp => resp.data)
}

