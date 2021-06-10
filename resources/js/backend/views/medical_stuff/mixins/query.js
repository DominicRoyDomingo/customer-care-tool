export function search_articles(params) {
    return axios.get(`/api/advance-search/articles`, { params }).then(resp => resp.data)
}

export function get_terms_by_types(params) {
    return axios.get(`/api/terms/terms-by-type`, { params }).then(resp => resp.data)
}

export function get_course_term_id(params) {
    return axios.get(`/api/terms/course-term-id`, { params }).then(resp => resp.data)
}

export function link_course_link(params) {
    return axios.post(`/api/terms/linke-course-term`, params).then(resp => resp.data)
}

export function get_course_terms(params) {
    return axios.get(`/api/links/terms`, { params }).then(resp => resp.data)
}


