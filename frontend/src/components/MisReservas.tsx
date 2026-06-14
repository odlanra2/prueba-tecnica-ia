import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../api/api';
import { Container } from 'react-bootstrap';
import './MisReservas.css';

interface Reservation {
  id: number;
  service: {
    name: string;
    duration: number;
    price: number;
    category?: string;
  };
  fecha_inicio: string;
  estado: string;
}

type TabKey = 'Activas' | 'Historial';

const getCategoryFromService = (service: Reservation['service']) => {
  if (service.category) return service.category;
  const name = service.name.toLowerCase();
  if (name.includes('legal') || name.includes('asesor')) return 'Legal';
  if (name.includes('coach')) return 'Coaching';
  return 'Consultoría';
};

const getCategoryIcon = (category: string) => {
  if (category === 'Consultoría') return '💼';
  if (category === 'Legal') return '⚖️';
  if (category === 'Coaching') return '🎯';
  return '📋';
};

const getCategoryIconClass = (category: string) => {
  if (category === 'Consultoría') return 'reserva-card-icon--consultoria';
  if (category === 'Legal') return 'reserva-card-icon--legal';
  return 'reserva-card-icon--coaching';
};

const formatPrice = (price: number) =>
  `$ ${price.toLocaleString('es-CO')}`;

const getStatusLabel = (estado: string) => {
  if (estado === 'active') return 'Activa';
  if (estado === 'cancelled') return 'Cancelada';
  return 'Disponible';
};

const getDaysUntil = (fechaInicio: string) => {
  const now = new Date();
  now.setHours(0, 0, 0, 0);
  const target = new Date(fechaInicio);
  target.setHours(0, 0, 0, 0);
  const diff = Math.ceil(
    (target.getTime() - now.getTime()) / (1000 * 60 * 60 * 24)
  );
  if (diff === 0) return 'Hoy';
  if (diff === 1) return 'En 1 día';
  if (diff < 0) return 'Pasada';
  return `En ${diff} días`;
};

const MisReservas: React.FC = () => {
  const { userId } = useParams<{ userId: string }>();
  const navigate = useNavigate();
  const [reservas, setReservas] = useState<Reservation[]>([]);
  const [tab, setTab] = useState<TabKey>('Activas');

  useEffect(() => {
    if (userId) {
      api
        .get('/reservations/filter', {
          params: {
            user_id: userId,
            from: '2026-01-01',
            to: '2026-12-31',
          },
        })
        .then(res => setReservas(res.data));
    }
  }, [userId]);

  const cancelarReserva = async (id: number) => {
    try {
      const res = await api.post(`/reservations/${id}/cancel`);
      alert(res.data.message + ' - Reembolso: ' + res.data.reembolso);
      setReservas(prev =>
        prev.map(r => (r.id === id ? { ...r, estado: 'cancelled' } : r))
      );
    } catch (err: any) {
      alert('Error al cancelar: ' + err.message);
    }
  };

  const activasCount = reservas.filter(r => r.estado === 'active').length;
  const canceladasCount = reservas.filter(r => r.estado === 'cancelled').length;
  const disponiblesCount = reservas.filter(r => r.estado === 'available').length;

  const filtradas = reservas.filter(r =>
    tab === 'Activas' ? r.estado === 'active' : r.estado === 'cancelled'
  );

  const goToServices = () => navigate(`/services/${userId}`);

  return (
    <div className="reservas-screen">
      <header className="reservas-header">
        <Container>
          <div className="reservas-header-inner">
            <div className="reservas-header-left">
              <button
                type="button"
                className="reservas-back-btn"
                aria-label="Volver a servicios"
                onClick={goToServices}
              >
                ←
              </button>
              <h1 className="reservas-title">Mis reservas</h1>
            </div>
            <button
              type="button"
              className="reservas-nueva-btn"
              onClick={goToServices}
            >
              + Nueva
            </button>
          </div>
          <hr className="reservas-divider" />
        </Container>
      </header>

      <Container>
        <div className="reservas-stats">
          <div className="reservas-stat-card">
            <div className="reservas-stat-value reservas-stat-value--active">
              {activasCount}
            </div>
            <p className="reservas-stat-label">Activas</p>
          </div>
          <div className="reservas-stat-card">
            <div className="reservas-stat-value reservas-stat-value--muted">
              {canceladasCount}
            </div>
            <p className="reservas-stat-label">Canceladas</p>
          </div>
          <div className="reservas-stat-card">
            <div className="reservas-stat-value reservas-stat-value--muted">
              {disponiblesCount}
            </div>
            <p className="reservas-stat-label">Disponibles</p>
          </div>
        </div>

        <div className="reservas-tabs">
          <button
            type="button"
            className={`reservas-tab${tab === 'Activas' ? ' reservas-tab--active' : ''}`}
            onClick={() => setTab('Activas')}
          >
            Activas
            {activasCount > 0 && (
              <span className="reservas-tab-badge">{activasCount}</span>
            )}
          </button>
          <button
            type="button"
            className={`reservas-tab${tab === 'Historial' ? ' reservas-tab--active' : ''}`}
            onClick={() => setTab('Historial')}
          >
            Historial
          </button>
        </div>

        {filtradas.length === 0 ? (
          <div className="reservas-empty">
            {tab === 'Activas'
              ? 'No tienes reservas activas.'
              : 'No hay reservas en el historial.'}
          </div>
        ) : (
          filtradas.map(r => {
            const fecha = new Date(r.fecha_inicio);
            const fechaStr = fecha.toLocaleDateString('es-CO', {
              weekday: 'short',
              day: 'numeric',
              month: 'short',
            });
            const horaStr = fecha.toLocaleTimeString('es-CO', {
              hour: '2-digit',
              minute: '2-digit',
            });
            const category = getCategoryFromService(r.service);
            const isActive = r.estado === 'active';

            return (
              <div
                key={r.id}
                className={`reserva-card${
                  isActive ? ' reserva-card--active' : ' reserva-card--cancelled'
                }`}
              >
                <div className="reserva-card-body">
                  <div className="reserva-card-top">
                    <div
                      className={`reserva-card-icon ${getCategoryIconClass(category)}`}
                    >
                      {getCategoryIcon(category)}
                    </div>
                    <div className="reserva-card-title-wrap">
                      <h3 className="reserva-card-title">{r.service.name}</h3>
                    </div>
                    <span
                      className={`reserva-status-badge${
                        isActive
                          ? ' reserva-status-badge--active'
                          : ' reserva-status-badge--cancelled'
                      }`}
                    >
                      {getStatusLabel(r.estado)}
                    </span>
                  </div>

                  <div className="reserva-card-meta">
                    <span className="reserva-card-meta-item">
                      📅 {fechaStr}
                    </span>
                    <span className="reserva-card-meta-item">🕐 {horaStr}</span>
                    <span className="reserva-card-meta-item">
                      ⏱ {r.service.duration} min
                    </span>
                  </div>

                  <div className="reserva-card-price-row">
                    <span className="reserva-card-price">
                      {formatPrice(r.service.price)}
                    </span>
                    {isActive && (
                      <span className="reserva-card-refund">
                        Reembolso 100% si cancelas
                      </span>
                    )}
                  </div>
                </div>

                {isActive && (
                  <div className="reserva-card-footer">
                    <span className="reserva-card-countdown">
                      {getDaysUntil(r.fecha_inicio)}
                    </span>
                    <button
                      type="button"
                      className="reserva-card-cancel"
                      onClick={() => cancelarReserva(r.id)}
                    >
                      Cancelar →
                    </button>
                  </div>
                )}
              </div>
            );
          })
        )}
      </Container>
    </div>
  );
};

export default MisReservas;
