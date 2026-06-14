import React from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import UsersScreen from '../components/UsersScreen';
import ServicesScreen from '../components/ServicesScreen';
import MisReservas from '../components/MisReservas';

export const AppRouter: React.FC = () => {
  return (
    <BrowserRouter>
      {/* Aquí puedes poner Navbar, Header, etc. */}
      <Routes>
        <Route path="/" element={<UsersScreen />} />
        <Route path="/services/:userId" element={<ServicesScreen />} />
        <Route path="/reservations/:userId" element={<MisReservas  />} />
        {/* Ejemplo de rutas protegidas */}
        <Route
          path="/admin"
          element={<Navigate replace to="/admin/dashboard" />}
        />
        {/* otras rutas */}
      </Routes>
    </BrowserRouter>
  );
};
