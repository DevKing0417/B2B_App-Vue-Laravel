import { defineStore } from 'pinia';
import axios, { AxiosError } from 'axios';
import useToastNotification from '../composables/useToast';

interface Campaign {
  id: number;
  title: string;
  description: string;
  target_amount: number;
  current_amount: number;
  status: 'active' | 'completed' | 'cancelled';
  end_date: string;
  image_url?: string;
  created_at: string;
  updated_at: string;
}

interface CampaignState {
  campaigns: Campaign[];
  currentCampaign: Campaign | null;
  isLoading: boolean;
  error: string | null;
}

export const useCampaignStore = defineStore('campaign', {
  state: (): CampaignState => ({
    campaigns: [],
    currentCampaign: null,
    isLoading: false,
    error: null,
  }),

  getters: {
    activeCampaigns: (state: CampaignState) => state.campaigns.filter((campaign: Campaign) => campaign.status === 'active'),
    completedCampaigns: (state: CampaignState) => state.campaigns.filter((campaign: Campaign) => campaign.status === 'completed'),
    cancelledCampaigns: (state: CampaignState) => state.campaigns.filter((campaign: Campaign) => campaign.status === 'cancelled'),
  },

  actions: {
    async fetchCampaigns() {
      this.isLoading = true;
      try {
        const response = await axios.get('/api/campaigns');
        this.campaigns = response.data;
      } catch (error) {
        const axiosError = error as AxiosError;
        this.error = axiosError.message;
        useToastNotification().showError('Failed to fetch campaigns');
      } finally {
        this.isLoading = false;
      }
    },

    async fetchCampaign(id: number) {
      this.isLoading = true;
      try {
        const response = await axios.get(`/api/campaigns/${id}`);
        this.currentCampaign = response.data;
      } catch (error) {
        const axiosError = error as AxiosError;
        this.error = axiosError.message;
        useToastNotification().showError('Failed to fetch campaign details');
      } finally {
        this.isLoading = false;
      }
    },

    async createCampaign(campaignData: Partial<Campaign>) {
      this.isLoading = true;
      try {
        const response = await axios.post('/api/campaigns', campaignData);
        this.campaigns.push(response.data);
        useToastNotification().showSuccess('Campaign created successfully');
        return response.data;
      } catch (error) {
        const axiosError = error as AxiosError;
        this.error = axiosError.message;
        useToastNotification().showError('Failed to create campaign');
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async updateCampaign(id: number, campaignData: Partial<Campaign>) {
      this.isLoading = true;
      try {
        const response = await axios.put(`/api/campaigns/${id}`, campaignData);
        const index = this.campaigns.findIndex(c => c.id === id);
        if (index !== -1) {
          this.campaigns[index] = response.data;
        }
        useToastNotification().showSuccess('Campaign updated successfully');
        return response.data;
      } catch (error) {
        const axiosError = error as AxiosError;
        this.error = axiosError.message;
        useToastNotification().showError('Failed to update campaign');
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async deleteCampaign(id: number) {
      this.isLoading = true;
      try {
        await axios.delete(`/api/campaigns/${id}`);
        this.campaigns = this.campaigns.filter(c => c.id !== id);
        useToastNotification().showSuccess('Campaign deleted successfully');
      } catch (error) {
        const axiosError = error as AxiosError;
        this.error = axiosError.message;
        useToastNotification().showError('Failed to delete campaign');
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async makeDonation(campaignId: number, amount: number, message?: string) {
      this.isLoading = true;
      try {
        const response = await axios.post(`/api/campaigns/${campaignId}/donate`, {
          amount,
          message
        });
        const campaign = this.campaigns.find(c => c.id === campaignId);
        if (campaign) {
          campaign.current_amount += amount;
        }
        useToastNotification().showSuccess('Donation successful! Thank you for your support.');
        return response.data;
      } catch (error) {
        const axiosError = error as AxiosError;
        this.error = axiosError.message;
        useToastNotification().showError('Failed to process donation');
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    clearError() {
      this.error = null;
    },
  },
}); 