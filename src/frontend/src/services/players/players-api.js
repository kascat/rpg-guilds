import { get, post, put, destroy } from 'boot/axios';

export const getPlayersTotal = async (params) => {
  const { data } = await get(`/players/total`, params);
  return data;
};

export const getPlayers = async (params = { page: '', rowsPerPage: 0 }) => {
  params.perPage = params.rowsPerPage || 0;
  const { data } = await get('/players', params);
  params.rowsNumber = data.total;
  return data.data;
};

export const getPlayer = async (id, params) => {
  const { data } = await get(`/players/${id}`, params);
  return data;
};

export const createPlayer = async item => {
  const { data } = await post('/players', item);
  return data;
};

export const updatePlayer = async (id, item) => {
  const { data } = await put(`/players/${id}`, item);
  return data;
};

export const destroyPlayer = async id => {
  await destroy(`/players/${id}`);
};
