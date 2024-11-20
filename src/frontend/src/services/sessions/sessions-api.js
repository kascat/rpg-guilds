import { get, post, put, destroy } from 'boot/axios';

export const getSessionsTotal = async (params) => {
  const { data } = await get(`/sessions/total`, params);
  return data;
};

export const getSessions = async (params = { page: '', rowsPerPage: 0 }) => {
  params.perPage = params.rowsPerPage || 0;
  const { data } = await get('/sessions', params);
  params.rowsNumber = data.total;
  return data.data;
};

export const getSession = async (id, params) => {
  const { data } = await get(`/sessions/${id}`, params);
  return data;
};

export const createSession = async item => {
  const { data } = await post('/sessions', item);
  return data;
};

export const updateSession = async (id, item) => {
  const { data } = await put(`/sessions/${id}`, item);
  return data;
};

export const destroySession = async id => {
  await destroy(`/sessions/${id}`);
};
