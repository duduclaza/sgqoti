const API_BASE_URL = 'http://localhost:5000/api';

class TonersService {
  async getAllToners() {
    try {
      const response = await fetch(`${API_BASE_URL}/toners`);
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || 'Erro ao buscar toners');
      }
      
      return data.data;
    } catch (error) {
      console.error('Erro ao buscar toners:', error);
      throw error;
    }
  }

  async getTonerById(id) {
    try {
      const response = await fetch(`${API_BASE_URL}/toners/${id}`);
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || 'Erro ao buscar toner');
      }
      
      return data.data;
    } catch (error) {
      console.error('Erro ao buscar toner:', error);
      throw error;
    }
  }

  async createToner(tonerData) {
    try {
      const response = await fetch(`${API_BASE_URL}/toners`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(tonerData),
      });
      
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || 'Erro ao criar toner');
      }
      
      return data.data;
    } catch (error) {
      console.error('Erro ao criar toner:', error);
      throw error;
    }
  }

  async updateToner(id, tonerData) {
    try {
      const response = await fetch(`${API_BASE_URL}/toners/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(tonerData),
      });
      
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || 'Erro ao atualizar toner');
      }
      
      return data;
    } catch (error) {
      console.error('Erro ao atualizar toner:', error);
      throw error;
    }
  }

  async deleteToner(id) {
    try {
      const response = await fetch(`${API_BASE_URL}/toners/${id}`, {
        method: 'DELETE',
      });
      
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || 'Erro ao excluir toner');
      }
      
      return data;
    } catch (error) {
      console.error('Erro ao excluir toner:', error);
      throw error;
    }
  }
}

export default new TonersService();
