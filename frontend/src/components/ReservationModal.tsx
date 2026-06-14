import React, { useState } from 'react';
import { Modal, Button } from 'react-bootstrap';
import DatePicker from 'react-datepicker';
import 'react-datepicker/dist/react-datepicker.css';
import { createReservation } from '../services/reservationService';
import './ReservationModal.css';

interface ServiceInfo {
  id: number;
  name: string;
  price: number;
  category: string;
}

interface Props {
  show: boolean;
  onHide: () => void;
  userId: number;
  service: ServiceInfo;
}

const getCategoryIcon = (category: string) => {
  if (category === 'Consultoría') return '💼';
  if (category === 'Legal') return '⚖️';
  if (category === 'Coaching') return '🎯';
  return '📋';
};

const formatPrice = (price: number) =>
  `$ ${price.toLocaleString('es-CO')}`;

const ReservationModal: React.FC<Props> = ({ show, onHide, userId, service }) => {
  const [fechaInicio, setFechaInicio] = useState<Date | null>(null);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async () => {
    if (!fechaInicio) {
      alert('Selecciona una fecha y hora');
      return;
    }

    try {
      setLoading(true);
      const fechaLocal = fechaInicio.toLocaleString('sv-SE');
      const result = await createReservation(userId, service.id, fechaLocal);
      alert(`Reserva creada con ID: ${result.id}`);
      onHide();
    } catch (err: any) {
      alert(`Error: ${err.message}`);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal
      show={show}
      onHide={onHide}
      centered
      className="reservation-modal"
      contentClassName="border-0"
    >
      <Modal.Header closeButton>
        <Modal.Title>Reservar servicio</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <div className="reservation-modal-service">
          <div className="reservation-modal-service-icon">
            {getCategoryIcon(service.category)}
          </div>
          <div>
            <div className="reservation-modal-service-name">{service.name}</div>
            <div className="reservation-modal-service-price">
              {formatPrice(service.price)}
            </div>
          </div>
        </div>

        <div className="reservation-modal-label">Selecciona fecha y hora</div>
        <DatePicker
          selected={fechaInicio}
          onChange={(date: Date | null) => setFechaInicio(date)}
          showTimeSelect
          timeIntervals={30}
          minDate={new Date()}
          dateFormat="dd/MM/yyyy h:mm aa"
          placeholderText="Elige fecha y hora"
          className="form-control"
          filterTime={(time) => {
            const now = new Date();
            const minDateTime = new Date(now.getTime() + 2 * 60 * 60 * 1000);
            const hour = time.getHours();
            return time >= minDateTime && hour >= 7 && hour < 19;
          }}
          filterDate={(date) => date.getDay() !== 0}
        />
        <p className="reservation-modal-hint">
          Mínimo 2 horas de anticipación · Horario 7:00–19:00 · Sin domingos
        </p>

        <button
          type="button"
          className="reservation-modal-btn-reservar"
          onClick={handleSubmit}
          disabled={loading}
        >
          {loading ? 'Guardando...' : 'Reservar →'}
        </button>
      </Modal.Body>
      <Modal.Footer className="reservation-modal-footer">
        <Button
          variant="link"
          className="reservation-modal-btn-cancel"
          onClick={onHide}
        >
          Cancelar
        </Button>
      </Modal.Footer>
    </Modal>
  );
};

export default ReservationModal;
