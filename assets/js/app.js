// SGQ OTI - JavaScript Configuration
// Arquivo criado para resolver erro 500 no módulo de toners

class SGQConfig {
    constructor() {
        this.environment = this.detectEnvironment();
        this.config = this.getConfig();
    }

    detectEnvironment() {
        const hostname = window.location.hostname;
        
        // Produção
        if (hostname === 'sgq.sgqoti.com.br') {
            return 'production';
        }
        
        // Desenvolvimento
        if (hostname === 'localhost' || hostname === '127.0.0.1' || hostname === '::1' || hostname.endsWith('.local')) {
            return 'development';
        }
        
        // Default para desenvolvimento
        return 'development';
    }

    getConfig() {
        if (this.environment === 'production') {
            return {
                baseUrl: 'https://sgq.sgqoti.com.br',
                apiUrl: 'https://sgq.sgqoti.com.br/api',
                assetsUrl: 'https://sgq.sgqoti.com.br/assets',
                debug: false
            };
        } else {
            return {
                baseUrl: 'http://localhost/SGQOTI',
                apiUrl: 'http://localhost/SGQOTI/api',
                assetsUrl: 'http://localhost/SGQOTI/assets',
                debug: true
            };
        }
    }

    url(path = '') {
        path = path.replace(/^\/+/, '');
        return this.config.baseUrl + (path ? '/' + path : '');
    }

    api(endpoint) {
        endpoint = endpoint.replace(/^\/+/, '');
        return this.config.apiUrl + '/' + endpoint;
    }

    asset(path) {
        path = path.replace(/^\/+/, '');
        return this.config.assetsUrl + '/' + path;
    }

    isDevelopment() {
        return this.environment === 'development';
    }

    isProduction() {
        return this.environment === 'production';
    }
}

// Instância global
const SGQ = new SGQConfig();

// Função helper para requisições
function sgqFetch(endpoint, options = {}) {
    const url = endpoint.startsWith('http') ? endpoint : SGQ.api(endpoint);
    
    const defaultOptions = {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    const mergedOptions = { ...defaultOptions, ...options };
    
    if (SGQ.isDevelopment()) {
        console.log('SGQ Fetch:', url, mergedOptions);
    }
    
    return fetch(url, mergedOptions);
}

// Log de inicialização
if (SGQ.isDevelopment()) {
    console.log('SGQ Config initialized:', SGQ.config);
}
