import api from '../api/api';

export const createReservation = async (
  userId: number,
  serviceId: number,
  fechaInicio: string
) => {
  try {
    const response = await api.post('/reservations', {
      user_id: userId,
      service_id: serviceId,
      fecha_inicio: fechaInicio,
    });
    return response.data;
  } catch (error: any) {
    if (error.response?.data?.message) {
      throw new Error(error.response.data.message);
    }
    throw new Error('Error al crear la reserva');
  }
};
