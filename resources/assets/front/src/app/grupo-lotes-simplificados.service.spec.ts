import { TestBed } from '@angular/core/testing';

import { GrupoLotesSimplificadosService } from './grupo-lotes-simplificados.service';

describe('GrupoLotesSimplificadosService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: GrupoLotesSimplificadosService = TestBed.get(GrupoLotesSimplificadosService);
    expect(service).toBeTruthy();
  });
});
