import { TestBed } from '@angular/core/testing';

import { LoteSalasSimplificadoService } from './lote-salas-simplificado.service';

describe('LoteSalasSimplificadoService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: LoteSalasSimplificadoService = TestBed.get(LoteSalasSimplificadoService);
    expect(service).toBeTruthy();
  });
});
