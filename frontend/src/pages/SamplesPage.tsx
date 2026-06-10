import { useState } from 'react';
import {
  Box, Typography, Table, TableBody, TableCell, TableContainer,
  TableHead, TableRow, Paper, Chip, TextField, Button, Dialog,
  DialogTitle, DialogContent, DialogActions,
} from '@mui/material';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { endpoints } from '../api/endpoints';
import type { Sample } from '../api/types';

const statusColors: Record<string, 'default' | 'primary' | 'secondary' | 'success' | 'warning' | 'info'> = {
  registered: 'info',
  in_progress: 'warning',
  completed: 'success',
  approved: 'primary',
  rejected: 'error',
};

export default function SamplesPage() {
  const [open, setOpen] = useState(false);
  const [barcode, setBarcode] = useState('');
  const queryClient = useQueryClient();

  const { data, isLoading } = useQuery({
    queryKey: ['samples'],
    queryFn: () => endpoints.samples.list(),
  });

  const createMutation = useMutation({
    mutationFn: (sample: Partial<Sample>) => endpoints.samples.create(sample),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['samples'] });
      setOpen(false);
      setBarcode('');
    },
  });

  const samples: Sample[] = data?.data?.['hydra:member'] || [];

  return (
    <Box>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
        <Typography variant="h4">Samples</Typography>
        <Button variant="contained" onClick={() => setOpen(true)}>Register Sample</Button>
      </Box>

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>Barcode</TableCell>
              <TableCell>Status</TableCell>
              <TableCell>Customer</TableCell>
              <TableCell>Type</TableCell>
              <TableCell>Created</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {isLoading && (
              <TableRow><TableCell colSpan={5}>Loading...</TableCell></TableRow>
            )}
            {!isLoading && samples.length === 0 && (
              <TableRow><TableCell colSpan={5}>No samples found</TableCell></TableRow>
            )}
            {samples.map((sample) => (
              <TableRow key={sample.id}>
                <TableCell>{sample.barcode}</TableCell>
                <TableCell>
                  <Chip label={sample.status} color={statusColors[sample.status] || 'default'} size="small" />
                </TableCell>
                <TableCell>{sample.customer?.name || '-'}</TableCell>
                <TableCell>{sample.sampleType?.name || '-'}</TableCell>
                <TableCell>{new Date(sample.createdAt).toLocaleDateString()}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>

      <Dialog open={open} onClose={() => setOpen(false)}>
        <DialogTitle>Register New Sample</DialogTitle>
        <DialogContent>
          <TextField
            autoFocus fullWidth label="Barcode" value={barcode}
            onChange={(e) => setBarcode(e.target.value)} sx={{ mt: 2 }}
          />
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpen(false)}>Cancel</Button>
          <Button onClick={() => createMutation.mutate({ barcode })} variant="contained">
            Create
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
}
