import { api, post } from 'boot/axios';

export const register = async (data) => {
  return await post('/register', data);
};

export const loginUser = async (data) => {
  return await post('/login', data);
};

export const forgotPassword = async (email) => {
  return await post('/forgot-password', { email });
};

export const resetPassword = async (passwords, token) => {
  return await api.post('/reset-password', passwords, { headers: { Authorization: `Bearer ${token}` } });
};

export const logoutUser = async () => {
  return await post('/logout', []);
};
