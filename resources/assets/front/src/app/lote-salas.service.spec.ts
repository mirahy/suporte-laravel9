import { TestBed } from '@angular/core/testing';

import { LoteSalasService } from './lote-salas.service';

describe('LoteSalasService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: LoteSalasService = TestBed.get(LoteSalasService);
    expect(service).toBeTruthy();
  });
});
