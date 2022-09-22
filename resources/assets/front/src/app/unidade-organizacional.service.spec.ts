import { TestBed } from '@angular/core/testing';

import { UnidadeOrganizacionalService } from './unidade-organizacional.service';

describe('UnidadeOrganizacionalService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: UnidadeOrganizacionalService = TestBed.get(UnidadeOrganizacionalService);
    expect(service).toBeTruthy();
  });
});
