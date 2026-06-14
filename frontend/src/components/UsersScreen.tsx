import React, { useEffect, useState } from 'react';
import api from '../api/api';
import { Card, Row, Col, Container } from 'react-bootstrap';
import { useNavigate } from 'react-router-dom';
import './UsersScreen.css';

interface User {
  id: number;
  name: string;
  plan: string;
  reservas_activas: number;
}

const AVATAR_COLORS = ['#4ecdc4', '#e85d75', '#f5a623', '#9b7ede'];

const getInitials = (name: string) =>
  name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase();

const UsersScreen: React.FC = () => {
  const [users, setUsers] = useState<User[]>([]);
  const navigate = useNavigate();

  useEffect(() => {
    api.get('/users').then(res => setUsers(res.data));
  }, []);

  const handleUserClick = (user: User) => {
    if (user.reservas_activas >= 3) return;
    navigate(`/services/${user.id}`);
  };

  return (
    <div className="users-screen">
      <header className="users-screen-header">
        <Container>
          <div className="d-flex justify-content-between align-items-center py-3">
            <span className="users-screen-brand">Reservas</span>
            <span className="users-screen-header-hint">Selecciona un usuario</span>
          </div>
          <hr className="users-screen-divider" />
        </Container>
      </header>

      <Container>
        <div className="users-screen-hero text-center">
          <h1 className="users-screen-title">¿Quién reserva hoy?</h1>
          <p className="users-screen-subtitle">
            Selecciona tu perfil para continuar
          </p>
        </div>

        <Row className="users-screen-grid g-3">
          {users.map((user, index) => {
            const initials = getInitials(user.name);
            const isLimitReached = user.reservas_activas >= 3;
            const isPremium = user.plan === 'premium';
            const avatarColor = AVATAR_COLORS[index % AVATAR_COLORS.length];

            return (
              <Col xs={6} key={user.id}>
                <Card
                  className={`user-card text-center h-100${isLimitReached ? ' user-card--disabled' : ''}`}
                  onClick={() => handleUserClick(user)}
                >
                  <Card.Body>
                    <div
                      className="user-avatar"
                      style={{ backgroundColor: avatarColor }}
                    >
                      {initials}
                    </div>
                    <Card.Title as="div" className="user-name">
                      {user.name}
                    </Card.Title>
                    <div
                      className={`user-badge${isPremium ? ' user-badge--premium' : ' user-badge--standard'}`}
                    >
                      {isPremium && (
                        <span className="user-badge-star" aria-hidden="true">
                          ★
                        </span>
                      )}
                      {isPremium ? 'Premium' : 'Estándar'}
                    </div>
                    <Card.Text
                      className={`user-reservations${isLimitReached ? ' user-reservations--limit' : ''}`}
                    >
                      {isLimitReached
                        ? 'Límite alcanzado'
                        : `${user.reservas_activas}/3 reservas`}
                    </Card.Text>
                  </Card.Body>
                </Card>
              </Col>
            );
          })}
        </Row>
      </Container>
    </div>
  );
};

export default UsersScreen;
