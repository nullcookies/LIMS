import apiClient from './client';
import type { Sample, Customer, SampleTest } from './types';

export const endpoints = {
  health: () => apiClient.get('/health'),

  login: (email: string, password: string) =>
    apiClient.post('/login', { email, password }, { headers: { 'Content-Type': 'application/json' } }),

  me: () => apiClient.get('/me'),

  samples: {
    list: (params?: Record<string, unknown>) =>
      apiClient.get('/samples', { params }),
    get: (id: number) => apiClient.get(`/samples/${id}`),
    create: (data: Partial<Sample>) =>
      apiClient.post('/samples', data),
    update: (id: number, data: Partial<Sample>) =>
      apiClient.patch(`/samples/${id}`, data),
  },

  testMethods: {
    list: () =>
      apiClient.get('/test_methods'),
  },

  instruments: {
    list: () =>
      apiClient.get('/instruments'),
  },

  customers: {
    list: () =>
      apiClient.get('/customers'),
    create: (data: Partial<Customer>) =>
      apiClient.post('/customers', data),
  },

  sampleTests: {
    list: (params?: Record<string, unknown>) =>
      apiClient.get('/sample_tests', { params }),
    create: (data: Partial<SampleTest>) =>
      apiClient.post('/sample_tests', data),
    update: (id: number, data: Partial<SampleTest>) =>
      apiClient.patch(`/sample_tests/${id}`, data),
  },
};
