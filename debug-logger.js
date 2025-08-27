/**
 * SGQ Debug Logger - Capturador de logs para debug
 */

class SGQDebugLogger {
    constructor() {
        this.logs = [];
        this.errors = [];
        this.networkRequests = [];
        this.startTime = new Date();
        this.init();
    }

    init() {
        console.log('🔍 SGQ Debug Logger iniciado');
        this.captureErrors();
        this.captureNetworkRequests();
        this.overrideConsole();
        this.addDebugButton();
    }

    captureErrors() {
        window.addEventListener('error', (event) => {
            this.errors.push({
                timestamp: new Date().toISOString(),
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                stack: event.error?.stack || 'N/A'
            });
        });

        window.addEventListener('unhandledrejection', (event) => {
            this.errors.push({
                timestamp: new Date().toISOString(),
                type: 'Promise Rejection',
                reason: event.reason?.toString() || 'Unknown'
            });
        });
    }

    captureNetworkRequests() {
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            const startTime = Date.now();
            const url = args[0];
            const options = args[1] || {};
            
            try {
                const response = await originalFetch(...args);
                const endTime = Date.now();
                
                this.networkRequests.push({
                    timestamp: new Date().toISOString(),
                    url: url,
                    method: options.method || 'GET',
                    status: response.status,
                    duration: endTime - startTime,
                    body: options.body || null
                });
                
                return response;
            } catch (error) {
                this.networkRequests.push({
                    timestamp: new Date().toISOString(),
                    url: url,
                    method: options.method || 'GET',
                    status: 'ERROR',
                    error: error.message
                });
                throw error;
            }
        };
    }

    overrideConsole() {
        const originalLog = console.log;
        console.log = (...args) => {
            this.logs.push({
                timestamp: new Date().toISOString(),
                level: 'LOG',
                message: args.map(arg => this.stringify(arg)).join(' ')
            });
            originalLog.apply(console, args);
        };
    }

    stringify(obj) {
        if (typeof obj === 'string') return obj;
        if (typeof obj === 'number' || typeof obj === 'boolean') return obj.toString();
        if (obj === null) return 'null';
        if (obj === undefined) return 'undefined';
        
        try {
            return JSON.stringify(obj, null, 2);
        } catch (e) {
            return obj.toString();
        }
    }

    addDebugButton() {
        const button = document.createElement('button');
        button.innerHTML = '🔍 Debug Log';
        button.style.cssText = `
            position: fixed; top: 10px; right: 10px; z-index: 9999;
            background: #dc2626; color: white; border: none;
            padding: 8px 12px; border-radius: 6px; font-size: 12px;
            cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        `;
        
        button.addEventListener('click', () => this.generateReport());
        document.body.appendChild(button);
    }

    captureDOMSnapshot() {
        return {
            timestamp: new Date().toISOString(),
            url: window.location.href,
            
            elements: {
                userForm: !!document.getElementById('user-registration-form'),
                userModal: !!document.getElementById('user-modal'),
                addUserBtn: !!document.getElementById('add-user-btn'),
                userName: !!document.getElementById('user-name'),
                userEmail: !!document.getElementById('user-email'),
                configTabs: document.querySelectorAll('.config-tab').length,
                activeTab: document.querySelector('.config-tab.active')?.dataset?.tab || 'none',
                usersTabVisible: !document.getElementById('users-tab')?.classList.contains('hidden')
            },
            
            app: {
                exists: !!window.app,
                currentModule: window.app?.currentModule || 'unknown',
                theme: window.app?.theme || 'unknown'
            }
        };
    }

    generateReport() {
        console.log('📊 Gerando relatório de debug...');
        
        const report = {
            metadata: {
                generated: new Date().toISOString(),
                duration: Date.now() - this.startTime.getTime(),
                url: window.location.href,
                userAgent: navigator.userAgent
            },
            
            domSnapshot: this.captureDOMSnapshot(),
            logs: this.logs,
            errors: this.errors,
            networkRequests: this.networkRequests,
            
            tests: {
                appInstance: !!window.app,
                formExists: !!document.getElementById('user-registration-form'),
                modalExists: !!document.getElementById('user-modal')
            }
        };
        
        console.log('='.repeat(80));
        console.log('📋 SGQ DEBUG REPORT');
        console.log('='.repeat(80));
        console.log(JSON.stringify(report, null, 2));
        console.log('='.repeat(80));
        
        // Copiar para clipboard
        if (navigator.clipboard) {
            navigator.clipboard.writeText(JSON.stringify(report, null, 2)).then(() => {
                alert('📋 Relatório copiado para clipboard!');
            });
        } else {
            alert('📋 Relatório gerado no console. Copie manualmente.');
        }
        
        return report;
    }
}

// Inicializar automaticamente quando a página carregar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.sgqDebugLogger = new SGQDebugLogger();
    });
} else {
    window.sgqDebugLogger = new SGQDebugLogger();
}
