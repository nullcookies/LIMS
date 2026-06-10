import React from 'react';
import { Grid, Card, CardContent, Typography, Box } from '@mui/material';
import { Science, Assignment, CheckCircle, Schedule } from '@mui/icons-material';

const stats = [
  { label: 'Total Samples', value: '156', icon: <Science />, color: '#1976d2' },
  { label: 'In Progress', value: '23', icon: <Schedule />, color: '#f57c00' },
  { label: 'Completed', value: '128', icon: <CheckCircle />, color: '#388e3c' },
  { label: 'Test Methods', value: '45', icon: <Assignment />, color: '#7b1fa2' },
];

export default function DashboardPage() {
  return (
    <Grid container spacing={3}>
      <Grid item xs={12}>
        <Typography variant="h4" gutterBottom>Dashboard</Typography>
      </Grid>
      {stats.map((stat) => (
        <Grid item xs={12} sm={6} md={3} key={stat.label}>
          <Card>
            <CardContent sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
              <Box sx={{ color: stat.color }}>{stat.icon}</Box>
              <Box>
                <Typography variant="h4">{stat.value}</Typography>
                <Typography color="text.secondary">{stat.label}</Typography>
              </Box>
            </CardContent>
          </Card>
        </Grid>
      ))}
    </Grid>
  );
}


