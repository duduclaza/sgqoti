// API Configuration and Service
const API_BASE_URL = 'http://localhost/SGQ/backend/api';

class ApiService {
  constructor() {
    this.baseURL = API_BASE_URL;
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    
    const config = {
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(url, config);
      
      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
      }
      
      return await response.json();
    } catch (error) {
      console.error('API Request failed:', error);
      throw error;
    }
  }

  // Toners API
  async getToners() {
    const response = await this.request('/toners/');
    return response.records || [];
  }

  async createToner(tonerData) {
    const response = await this.request('/toners/', {
      method: 'POST',
      body: JSON.stringify(tonerData),
    });
    return response.toner;
  }

  async updateToner(id, tonerData) {
    const response = await this.request(`/toners/single.php?id=${id}`, {
      method: 'PUT',
      body: JSON.stringify(tonerData),
    });
    return response.toner;
  }

  async deleteToner(id) {
    await this.request(`/toners/single.php?id=${id}`, {
      method: 'DELETE',
    });
    return true;
  }

  async getToner(id) {
    return await this.request(`/toners/single.php?id=${id}`);
  }

  // Test connection
  async testConnection() {
    return await this.request('/test-connection.php');
  }
}

export default new ApiService();
