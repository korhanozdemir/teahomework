import axios from 'axios'

let instance = axios.create({
    timeout: 20000,
    headers: {
        'Authorization': 'Bearer xxx',
        'Content-Type': 'application/json',
    }
});

instance.interceptors.request.use(function (config) {
    console.log('Request yapıldı (' + config.method + ')', config.baseUrl + config.url);
    return config;
});

instance.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        const {status, statusText, data} = error.response;
        toastr.error(data.message, 'Beklenmedik Bir Hata Oluştu!');
        return Promise.reject(error);
    });


export default instance
