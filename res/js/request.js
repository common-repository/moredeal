const service = axios.create({
    headers: {
        'Content-Type': 'application/json',
    },
    baseURL: REST_URL.restUrl+'/seastar/v1',
    timeout: 10000,
    withCredentials: true
});
service.interceptors.request.use(
    config => {
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

service.interceptors.response.use(
    response => {
        if (response.data && response.data.success === true) {
            return response.data;
        } else if(response.data && response.data) {
            return response.data;
        }
    },
    error => {
        return Promise.reject(error);
    }
);
