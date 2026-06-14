import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import api from '../api/api';
import { Card, Container } from 'react-bootstrap';
import ReservationModal from './ReservationModal';
import './ServicesScreen.css';

interface User {
  id: number;
  name: string;
  plan: string;
  reservas_activas: number;
}

interface Service {
  id: number;
  name: string;
  description: string;
  duration: number;
  category: string;
  price: number;
}

const AVATAR_COLORS = ['#4ecdc4', '#e85d75', '#f5a623', '#9b7ede'];

const CATEGORIES = [
  { key: 'Todos', icon: '' },
  { key: 'Consultoría', icon: '💼' },
  { key: 'Legal', icon: '⚖️' },
  { key: 'Coaching', icon: '🎯' },
] as const;

const getInitials = (name: string) =>
  name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase();

const getShortName = (name: string) => {
  const parts = name.split(' ');
  if (parts.length < 2) return name;
  return `${parts[0]} ${parts[1][0]}.`;
};

const formatPrice = (price: number) =>
  `$ ${price.toLocaleString('es-CO')}`;

const getCategoryIcon = (category: string) => {
  if (category === 'Consultoría') return '💼';
  if (category === 'Legal') return '⚖️';
  if (category === 'Coaching') return '🎯';
  return '📋';
};

const getCategoryIconClass = (category: string) => {
  if (category === 'Consultoría') return 'service-card-icon--consultoria';
  if (category === 'Legal') return 'service-card-icon--legal';
  return 'service-card-icon--coaching';
};

const getCategoryTagClass = (category: string) => {
  if (category === 'Consultoría') return 'service-tag--consultoria';
  if (category === 'Legal') return 'service-tag--legal';
  return 'service-tag--coaching';
};

const ServicesScreen: React.FC = () => {
  const { userId } = useParams();
  const navigate = useNavigate();
  const [services, setServices] = useState<Service[]>([]);
  const [user, setUser] = useState<User | null>(null);
  const [filter, setFilter] = useState<string>('Todos');
  const [selectedService, setSelectedService] = useState<Service | null>(null);

  useEffect(() => {
    Promise.all([api.get('/services'), api.get('/users')]).then(
      ([servicesRes, usersRes]) => {
        setServices(servicesRes.data);
        const currentUser = usersRes.data.find(
          (u: User) => u.id === Number(userId)
        );
        setUser(currentUser ?? null);
      }
    );
  }, [userId]);

  const filteredServices =
    filter === 'Todos'
      ? services
      : services.filter(s => s.category === filter);

  const avatarColor = user
    ? AVATAR_COLORS[(user.id - 1) % AVATAR_COLORS.length]
    : AVATAR_COLORS[0];
  const disponibles = user ? Math.max(0, 3 - user.reservas_activas) : 0;
  const isPremium = user?.plan === 'premium';

  return (
    <div className="services-screen">
      <header className="services-screen-header">
        <Container>
          <div className="services-screen-header-inner">
            <div className="services-screen-header-left">
              <button
                type="button"
                className="services-back-btn"
                aria-label="Volver al inicio"
                onClick={() => navigate('/')}
              >
                ←
              </button>
              <span className="services-screen-brand">ReservasPro</span>
            </div>

            {user && (
              <div className="services-header-user">
                <div
                  className="services-header-avatar"
                  style={{ backgroundColor: avatarColor }}
                >
                  {getInitials(user.name)}
                </div>
                <div className="d-flex flex-column min-w-0">
                  <span className="services-header-name">
                    {getShortName(user.name)}
                  </span>
                  {isPremium && (
                    <span className="services-header-plan">
                      <span aria-hidden="true">★</span> Premium
                    </span>
                  )}
                </div>
              </div>
            )}
          </div>
          <hr className="services-screen-divider" />
        </Container>
      </header>

      <Container>
        <div className="services-nav-actions">
          <button
            type="button"
            className="services-nav-btn services-nav-btn--outline"
            onClick={() => navigate('/')}
          >
            ← Inicio
          </button>
          <button
            type="button"
            className="services-nav-btn services-nav-btn--primary"
            onClick={() => navigate(`/reservations/${userId}`)}
          >
            Mis reservas →
          </button>
        </div>

        {user && (
          <Card className="services-user-card mb-4">
            <Card.Body>
              <div
                className="services-user-card-avatar"
                style={{ backgroundColor: 'rgba(255,255,255,0.2)' }}
              >
                {getInitials(user.name)}
              </div>
              <div className="services-user-card-info">
                <div className="services-user-card-name">{user.name}</div>
                <div className="services-user-card-meta">
                  <span>{user.reservas_activas}/3 reservas activas</span>
                  {isPremium && (
                    <span className="services-user-card-plan">
                      <span aria-hidden="true">★</span> Premium
                    </span>
                  )}
                </div>
              </div>
              <div className="services-user-card-stats">
                <div className="services-user-card-stats-label">Disponibles</div>
                <div className="services-user-card-stats-value">{disponibles}</div>
              </div>
            </Card.Body>
          </Card>
        )}

        <div className="services-section-header">
          <h2 className="services-section-title">Servicios</h2>
          <span className="services-section-count">
            {filteredServices.length} disponibles
          </span>
        </div>

        <div className="services-filters">
          {CATEGORIES.map(cat => (
            <button
              key={cat.key}
              type="button"
              className={`services-filter-btn${
                filter === cat.key ? ' services-filter-btn--active' : ''
              }`}
              onClick={() => setFilter(cat.key)}
            >
              {cat.icon && <span aria-hidden="true">{cat.icon}</span>}
              {cat.key}
            </button>
          ))}
        </div>

        {filteredServices.map(service => (
          <Card key={service.id} className="service-card">
            <Card.Body>
              <div className="service-card-top">
                <div
                  className={`service-card-icon ${getCategoryIconClass(service.category)}`}
                >
                  {getCategoryIcon(service.category)}
                </div>
                <div className="service-card-title-wrap">
                  <Card.Title as="div" className="service-card-title">
                    {service.name}
                  </Card.Title>
                </div>
                <span className="service-card-price">
                  {formatPrice(service.price)}
                </span>
              </div>
              <Card.Text className="service-card-description">
                {service.description}
              </Card.Text>
              <div className="service-card-tags">
                <span className="service-tag service-tag--duration">
                  🕐 {service.duration} min
                </span>
                <span
                  className={`service-tag ${getCategoryTagClass(service.category)}`}
                >
                  {service.category}
                </span>
              </div>
            </Card.Body>
            <div className="service-card-footer">
              <span className="service-card-hint">Toca para ver detalle</span>
              <button
                type="button"
                className="service-card-reserve"
                onClick={() => setSelectedService(service)}
              >
                Reservar →
              </button>
            </div>
          </Card>
        ))}
      </Container>

      {selectedService && userId && (
        <ReservationModal
          show
          onHide={() => setSelectedService(null)}
          userId={Number(userId)}
          service={selectedService}
        />
      )}
    </div>
  );
};

export default ServicesScreen;
