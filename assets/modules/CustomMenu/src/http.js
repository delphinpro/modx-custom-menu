/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

const ajaxUrl = APP.moduleUrl + '&action=';
export const url = actionName => ajaxUrl + actionName;

const defaultConfig = {
    credentials: 'same-origin',
    headers    : { 'X-Requested-With': 'XMLHttpRequest' },
    method     : 'POST',
    body       : null,
};

const setDefaultOption = (param, value) => {
    if (defaultConfig.hasOwnProperty(param)) {
        defaultConfig[param] = value;
    } else {
        console.warn(`Http(): unknown option: `, param);
    }
};

function checkStatus(response) {
    if (response.status >= 200 && response.status < 300) {
        return response;
    } else {
        let error = new Error(response.statusText);
        error.response = response;
        throw error;
    }
}

function parseJSON(response) {
    return response.json();
}

export function Http(url, fd, options) {
    fd = fd || null;
    options = options || defaultConfig;

    if (fd !== null) {
        if (!(fd instanceof FormData)) {
            if (typeof fd === 'object') {
                let nd = new FormData;
                for (let param in fd) {
                    if (fd.hasOwnProperty(param)) {
                        nd.append(param, fd[param]);
                    }
                }
                fd = nd;
            } else {
                throw new Error(`Http(): Invalid parameter, FormData or Map expected`);
            }
        }
    }

    let _options = {
        ...options,
        body: fd,
    };

    if (!url) {
        throw new Error(`Http(): Invalid url (should not be empty)`);
    }

    console.log(_options.method, url, _options);

    return fetch(url, _options)
        .then(checkStatus)
        .then(parseJSON)
        .then(response => {
            console.log('RESPONSE:', response);
            if (typeof response['status'] === 'undefined') {
                return response;
            }

            if (response['status'] === true) {
                return response;
            }

            let error = new Error(response.message || 'Server response isn\'t OK');
            error.response = response;

            throw error;
        });
}

export default Http;
