<template>
  <div class="space-y-6">
    <!-- General Settings -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">General Settings</h3>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <div class="sm:col-span-4">
            <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
            <div class="mt-1">
              <input
                type="text"
                id="company_name"
                v-model="settings.companyName"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="sm:col-span-4">
            <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
            <div class="mt-1">
              <input
                type="email"
                id="contact_email"
                v-model="settings.contactEmail"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="sm:col-span-4">
            <label for="min_donation" class="block text-sm font-medium text-gray-700">Minimum Donation Amount</label>
            <div class="mt-1">
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input
                  type="number"
                  id="min_donation"
                  v-model="settings.minDonation"
                  min="0"
                  step="0.01"
                  class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Campaign Settings -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Campaign Settings</h3>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <div class="sm:col-span-4">
            <label for="max_campaign_duration" class="block text-sm font-medium text-gray-700">Maximum Campaign Duration (days)</label>
            <div class="mt-1">
              <input
                type="number"
                id="max_campaign_duration"
                v-model="settings.maxCampaignDuration"
                min="1"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
            </div>
          </div>

          <div class="sm:col-span-4">
            <label for="max_campaign_target" class="block text-sm font-medium text-gray-700">Maximum Campaign Target Amount</label>
            <div class="mt-1">
              <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input
                  type="number"
                  id="max_campaign_target"
                  v-model="settings.maxCampaignTarget"
                  min="0"
                  step="0.01"
                  class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
              </div>
            </div>
          </div>

          <div class="sm:col-span-4">
            <label for="campaign_categories" class="block text-sm font-medium text-gray-700">Campaign Categories</label>
            <div class="mt-1">
              <textarea
                id="campaign_categories"
                v-model="settings.campaignCategories"
                rows="3"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="Enter categories, one per line"
              ></textarea>
              <p class="mt-2 text-sm text-gray-500">Enter one category per line</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Email Settings -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Email Settings</h3>
        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
          <div class="sm:col-span-4">
            <label for="donation_confirmation_template" class="block text-sm font-medium text-gray-700">Donation Confirmation Template</label>
            <div class="mt-1">
              <textarea
                id="donation_confirmation_template"
                v-model="settings.donationConfirmationTemplate"
                rows="6"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              ></textarea>
            </div>
          </div>

          <div class="sm:col-span-4">
            <label for="campaign_approval_template" class="block text-sm font-medium text-gray-700">Campaign Approval Template</label>
            <div class="mt-1">
              <textarea
                id="campaign_approval_template"
                v-model="settings.campaignApprovalTemplate"
                rows="6"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              ></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
      <button
        type="button"
        @click="saveSettings"
        :disabled="isSaving"
        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <span v-if="isSaving" class="mr-2">
          <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </span>
        {{ isSaving ? 'Saving...' : 'Save Settings' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

const settings = ref({
  companyName: '',
  contactEmail: '',
  minDonation: 1,
  maxCampaignDuration: 90,
  maxCampaignTarget: 10000,
  campaignCategories: '',
  donationConfirmationTemplate: 'Dear {{donor_name}},\n\nThank you for your generous donation of ${{amount}} to the "{{campaign_title}}" campaign.\n\nYour support makes a difference!\n\nBest regards,\n{{company_name}}',
  campaignApprovalTemplate: 'Dear {{creator_name}},\n\nYour campaign "{{campaign_title}}" has been approved and is now live on our platform.\n\nThank you for your initiative!\n\nBest regards,\n{{company_name}}',
});

const isSaving = ref(false);

const saveSettings = async () => {
  try {
    isSaving.value = true;
    // Save settings to API
    // await axios.put('/api/admin/settings', settings.value);
    alert('Settings saved successfully!');
  } catch (error) {
    console.error('Failed to save settings:', error);
    alert('Failed to save settings. Please try again.');
  } finally {
    isSaving.value = false;
  }
};

onMounted(async () => {
  try {
    // Fetch current settings
    // const response = await axios.get('/api/admin/settings');
    // settings.value = response.data;
  } catch (error) {
    console.error('Failed to fetch settings:', error);
    alert('Failed to load settings. Please try again.');
  }
});
</script> 