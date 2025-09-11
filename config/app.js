/**
 * Configurações JavaScript para SGQ OTI
 * Detecta automaticamente o ambiente e configura URLs
 */

class SGQConfig {
    constructor() {
        this.detectEnvironment();
    }
    
    detectEnvironment() {
        const host = window.location.hostname;
        
        if (host === 'localhost' || 
            host === '127.0.0.1' || 
            host === '::1' || 
            host.includes('.local')) {
            
            // Ambiente de desenvolvimento
            this.environment = 'development';
            this.baseUrl = window.location.origin + '/SGQOTI';
            this.apiUrl = this.baseUrl + '/api';
            this.assetsUrl = this.baseUrl + '/assets';
        } else {
            // Ambiente de produção
            this.environment = 'production';
            this.baseUrl = 'https://sgq.sgqoti.com.br';
            this.apiUrl = this.baseUrl + '/api';
            this.assetsUrl = this.baseUrl + '/assets';
        }
    }
    
    isDevelopment() {
        return this.environment === 'development';
    }
    
    isProduction() {
        return this.environment === 'production';
    }
    
    url(path = '') {
        path = path.replace(/^\/+/, '');
        return this.baseUrl + (path ? '/' + path : '');
    }
    
    api(endpoint) {
        endpoint = endpoint.replace(/^\/+/, '');
        return this.apiUrl + '/' + endpoint;
    }
    
    asset(path) {
        path = path.replace(/^\/+/, '');
        return this.assetsUrl + '/' + path;
    }
}

// Instância global
window.SGQ = new SGQConfig();

// Função helper para fazer requisições à API
window.sgqFetch = function(endpoint, options = {}) {
    const url = window.SGQ.api(endpoint);
    
    // Configurações padrão
    const defaultOptions = {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    // Merge das opções
    const finalOptions = Object.assign({}, defaultOptions, options);
    
    return fetch(url, finalOptions);
};

// Log do ambiente atual (apenas em desenvolvimento)
if (window.SGQ.isDevelopment()) {
    console.log('SGQ OTI - Ambiente:', window.SGQ.environment);
    console.log('Base URL:', window.SGQ.baseUrl);
    console.log('API URL:', window.SGQ.apiUrl);
}
