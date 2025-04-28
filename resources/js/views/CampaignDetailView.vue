<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div v-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <ExclamationCircleIcon class="h-5 w-5 text-red-400" />
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">{{ error }}</p>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="campaign" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Campaign Header -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="relative h-64">
          <img
            v-if="campaign.image_url"
            :src="campaign.image_url"
            class="w-full h-full object-cover"
            :alt="campaign.title"
          />
          <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
            <PhotographIcon class="h-12 w-12 text-gray-400" />
          </div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-6">
            <h1 class="text-3xl font-bold text-white">{{ campaign.title }}</h1>
            <p class="mt-2 text-white/90">{{ campaign.description }}</p>
          </div>
        </div>

        <div class="p-6">
          <!-- Progress Bar -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-gray-700">Progress</span>
              <span class="text-sm font-medium text-gray-900">
                {{ ((campaign.current_amount / campaign.target_amount) * 100).toFixed(1) }}%
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-indigo-600 h-2 rounded-full"
                :style="{ width: `${(campaign.current_amount / campaign.target_amount) * 100}%` }"
              ></div>
            </div>
            <div class="mt-2 flex items-center justify-between text-sm">
              <span class="text-gray-500">Raised: ${{ campaign.current_amount.toLocaleString() }}</span>
              <span class="text-gray-500">Target: ${{ campaign.target_amount.toLocaleString() }}</span>
            </div>
          </div>

          <!-- Campaign Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">Campaign Details</h3>
              <dl class="grid grid-cols-1 gap-2">
                <div class="flex items-center">
                  <CalendarIcon class="h-5 w-5 text-gray-400 mr-2" />
                  <dt class="text-sm font-medium text-gray-500">End Date:</dt>
                  <dd class="ml-2 text-sm text-gray-900">{{ formatDate(campaign.end_date) }}</dd>
                </div>
                <div class="flex items-center">
                  <ClockIcon class="h-5 w-5 text-gray-400 mr-2" />
                  <dt class="text-sm font-medium text-gray-500">Status:</dt>
                  <dd class="ml-2">
                    <span
                      :class="[
                        'px-2 py-1 text-xs font-semibold rounded-full',
                        campaign.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      ]"
                    >
                      {{ campaign.status }}
                    </span>
                  </dd>
                </div>
              </dl>
            </div>

            <!-- Donation Form -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Make a Donation</h3>
              <form @submit.prevent="handleDonation" class="space-y-4">
                <div>
                  <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input
                      type="number"
                      id="amount"
                      v-model="donationAmount"
                      min="1"
                      step="0.01"
                      class="block w-full pl-7 pr-12 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      placeholder="0.00"
                      required
                    />
                  </div>
                </div>

                <div>
                  <label for="message" class="block text-sm font-medium text-gray-700">Message (Optional)</label>
                  <textarea
                    id="message"
                    v-model="donationMessage"
                    rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Add a message of support..."
                  ></textarea>
                </div>

                <button
                  type="submit"
                  :disabled="isDonating"
                  class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <span v-if="isDonating" class="mr-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </span>
                  {{ isDonating ? 'Processing...' : 'Donate Now' }}
                </button>
              </form>
            </div>
          </div>

          <!-- Recent Donations -->
          <div v-if="donations.length > 0">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Donations</h3>
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
              <ul class="divide-y divide-gray-200">
                <li v-for="donation in donations" :key="donation.id" class="px-6 py-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ donation.user.name }}</p>
                      <p v-if="donation.message" class="mt-1 text-sm text-gray-500">{{ donation.message }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-medium text-indigo-600">${{ donation.amount.toLocaleString() }}</p>
                      <p class="mt-1 text-xs text-gray-500">{{ formatDate(donation.created_at) }}</p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCampaignStore } from '@/stores/campaign';
import { useAuthStore } from '@/stores/auth';
import {
  CalendarIcon,
  ClockIcon,
  PhotographIcon,
  ExclamationCircleIcon,
} from '@heroicons/vue/24/outline';
import { format } from 'date-fns';

const route = useRoute();
const router = useRouter();
const campaignStore = useCampaignStore();
const authStore = useAuthStore();

const isLoading = ref(true);
const isDonating = ref(false);
const error = ref<string | null>(null);
const campaign = ref<any>(null);
const donations = ref<any[]>([]);
const donationAmount = ref('');
const donationMessage = ref('');

const formatDate = (date: string) => {
  return format(new Date(date), 'MMM d, yyyy');
};

const handleDonation = async () => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }

  try {
    isDonating.value = true;
    const amount = parseFloat(donationAmount.value);
    const response = await campaignStore.makeDonation(campaign.value.id, amount);
    
    // Update campaign data
    campaign.value = response.data;
    
    // Add new donation to list
    donations.value.unshift({
      id: Date.now(),
      user: {
        name: authStore.user?.name,
      },
      amount,
      message: donationMessage.value,
      created_at: new Date().toISOString(),
    });

    // Reset form
    donationAmount.value = '';
    donationMessage.value = '';

    // Show success message
    alert('Thank you for your donation!');
  } catch (error) {
    console.error('Failed to make donation:', error);
    alert('Failed to process donation. Please try again.');
  } finally {
    isDonating.value = false;
  }
};

onMounted(async () => {
  try {
    const campaignId = parseInt(route.params.id as string);
    await campaignStore.fetchCampaign(campaignId);
    campaign.value = campaignStore.currentCampaign;
    
    // Fetch donations (you'll need to implement this in your API)
    // const response = await axios.get(`/api/campaigns/${campaignId}/donations`);
    // donations.value = response.data.data;
  } catch (error) {
    console.error('Failed to fetch campaign:', error);
    error.value = 'Failed to load campaign details';
  } finally {
    isLoading.value = false;
  }
});
</script> 